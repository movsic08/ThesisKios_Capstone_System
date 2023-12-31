<?php

namespace App\Http\Controllers;

use App\Events\NewUserCreated;
use App\Models\BachelorDegree;
use App\Models\DocuPost;
use App\Models\LoginLog;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register()
    {
        return view('user_pages.signup');
    }

    //creating account

    public function create(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'min:4', Rule::unique('users', 'username')],
            'first_name' => ['required', 'min:2'],
            'last_name' => ['required', 'min:2'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:8',
            'role_id' => 'required',
        ], [
            'first_name.required' => 'The first name field is required.',
            'fname.min' => 'The first name must be at least :min characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.min' => 'The last name must be at least :min characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least :min characters.',
            'role_id.required' => 'The role field is required.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // account role filtering
        if ($validated['role_id'] === 'student') {
            $validated['role_id'] = 1;
        } elseif ($validated['role_id'] === 'faculty') {
            $validated['role_id'] = 2;
        }

        $user = User::create($validated);
        auth()->login($user);

        $notificationNewAccount = Notification::create([
            'user_id' => auth()->user()->id,
            'header_message' => 'Complete acount information',
            'content_message' => 'Click here to complete your account information.',
            'link' => route('edit-profile', ['activeTab' => 'tab1']),
            'category' => 'system',
        ]);

        if ($notificationNewAccount) {
            return redirect()->route('home')->with('message', 'Creating new account success, finish setup you account');
        } else {
            return 'Account created but notification is not. Contact admin.';
        }

    }

    //logout

    public function logout(Request $request)
    {
        $is_admin = auth()->user()->is_admin;
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on the is_admin value
        if ($is_admin) {
            return redirect()->route('login')->with('message', 'Log out successfully.');
        } else {
            return redirect()->route('home')->with('message', 'Log out successfully.');
        }
    }

    //login

    public function login()
    {
        return view('user_pages.login');
    }

    //loginProcess

    public function loginProcess(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email. Create a new account.',
            ])->onlyInput('email');
        }

        $remember = $request->has('remember_me');

        //check if the user click remember me
        // dd($remember);

        if (auth()->attempt($validated, $remember)) {
            if ($remember) {
                // Set cookies for email and password if "Remember Me" is checked
                setcookie("email", $validated['email']);
                setcookie("password", $validated['password']);
            } else {
                setcookie("email", "");
                setcookie("password", "");
            }

            $request->session()->regenerate();
            $user = auth()->user();

            $existingLog = LoginLog::where('user_id', auth()->user()->id)
                ->whereDate('login_time', Carbon::today())
                ->first();

            // If no existing log, create a new one
            if (!$existingLog) {
                LoginLog::create([
                    'user_id' => auth()->user()->id,
                    'login_time' => now(),
                    'is_admin' => auth()->user()->is_admin == 1
                ]);
            }

            if ($user->is_admin) {
                return redirect()->route('admin-home')->with('message', 'Welcome back, Admin ' . $user->email . '!');
            } else {
                return redirect()->intended(route('home'))->with('message', 'Welcome back, ' . $user->email . '!');
            }
        }

        return back()->withErrors(['email' => 'You entered invalid credentials'])->onlyInput('email');
    }

    // show all student

    public function studentList()
    {
        $users = User::all();
        $bachelor_degree = BachelorDegree::all();
        // dd( $users );
        return view('admin.pages.user-list', compact('users', 'bachelor_degree'))->with('title', 'List of users');
    }

    public function addNewUser(Request $request)
    {
        // dd( $request );
        $validated = $request->validate([
            'username' => ['required', 'min:4'],
            'email' => ['required', 'email'],
            'password' => 'required|confirmed|min:8',
            'account_level' => 'required',
            // 'role_id' => 'required',
        ]);
        $validated['password'] = Hash::make($validated['password']);

        // Account role filtering
        if ($validated['account_level'] === 'user') {
            $is_admin = false;
        } elseif ($validated['account_level'] === 'admin') {
            $is_admin = true;
        }

        // Merge the calculated values into the validated data
        $validated['is_admin'] = $is_admin;

        //creation of user
        $user = User::create($validated);
        auth()->login($user);
        return redirect()->route('home')->with('message', 'Creating new account success, finish setup you account');

    }

    public function viewUser($username)
    {
        // dd( $username );

        $checkedAccount = User::where('username', $username)
            ->orWhere('id', $username)
            ->first();

        if ($checkedAccount == !null) {
            $currentUserId = $checkedAccount->id;
            $docuPostOfUser = DocuPost::where('user_id', $checkedAccount->id)->get();
            $fullName = $checkedAccount->first_name . ' ' . $checkedAccount->last_name;
        } else {
            $checkedAccount = null;
            $currentUserId = 'Unknown';
            $fullName = 'Unknown';
            $docuPostOfUser = null;
        }



        // dd( $docuPostOfUser );
        return view('user_pages.profile', [
            'currentUserId' => $currentUserId,
            'checkedAccount' => $checkedAccount,
            'fullName' => $fullName,
            'docuPostOfUser' => $docuPostOfUser,
            'username' => $username,
        ]);

    }

}
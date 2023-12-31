<div class="fixed inset-0 z-50 flex h-screen w-screen items-center justify-center">
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="mx-auto w-fit rounded-lg bg-white text-center text-gray-600 drop-shadow-lg">
            <div class="rounded-t-xl bg-primary-color p-8 px-10 py-3 font-semibold text-white">
                <h1>Retrieving users</h1>
            </div>
            <div class="flex flex-col gap-2 px-10 pb-8 pt-3">
                <img class="h-10" src="{{ asset('icons/logo.svg') }}" alt="Icon Description">
                <h3 class="text-xs md:text-base">Please wait while we are gathering users on your system.</h3>
                <div class="flex flex-col items-center justify-center gap-2">
                    <div class="h-8 w-8 animate-spin rounded-md border-4 border-t-4 border-blue-500">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

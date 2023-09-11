<?php

namespace App\Livewire;

use App\Models\DocuPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\BachelorDegree;
use Livewire\WithFileUploads;

class UploadDocument extends Component {
    use WithFileUploads;
    public $user, $bachelor_degree_value, $author1;

    public function mount() {
        $this->user = Auth::user();

        if ( $this->user->is_admin ) {
            $this->bachelor_degree_value = '';
        } else {
            $bachelor_degree_int = BachelorDegree::find( $this->user->bachelor_degree );
            $this->bachelor_degree_value = $bachelor_degree_int->name;
            $this->author1 = $this->user->first_name . ' ' . $this->user->last_name;
        }
    }

    public $currentTab = 3;

    public $title, $format, $document_type, $date_of_approval, $physical_description, $language, $panel_chair, $advisor, $panel_member1, $panel_member2, $panel_member3, $panel_member4, $abstract_or_summary, $author2, $author3, $author4;
    public $keyword1, $keyword2, $keyword3, $keyword4, $keyword5, $keyword6, $keyword7, $keyword8, $recommended_citation, $user_upload,  $pdf_path;

    protected $tab1Rules = [
        'title' => 'required|min:3',
        'document_type' => 'required|min:3',
        'date_of_approval' => 'required|date',
        'format' => 'required|min:2',
        'physical_description' => 'required|min:6',
        'language' => 'required|min:3',
        'panel_chair' => 'required|min:3',
        'advisor' => 'required|min:3',
        'bachelor_degree_value' => 'required',
        'panel_member1' => 'required|min:3',
        'panel_member2' => 'required|min:3',
        'panel_member3' => 'required|min:3',
        'panel_member4' => 'required|min:3',
    ];

    protected $tab2Rules = [
        'abstract_or_summary' => 'required',
        'keyword1' => 'required|min:1',
        'keyword2' => 'required|min:1',
        'keyword3' => 'required|min:1',
        'keyword4' => 'required|min:1',
        'recommended_citation' => 'required|min:10',
    ];

    protected $tab3Rules = [
        'user_upload' => 'required|file',
    ];

    public function changeTab( $tab ) {
        // Manually validate the fields based on the current tab
        if ( $this->currentTab == 1 ) {
            $this->validate( $this->tab1Rules );
        } elseif ( $this->currentTab == 2 ) {
            $this->validate( $this->tab2Rules );
        } elseif ( $this->currentTab == 3 ) {
            $this->validate( $this->tab3Rules );
        }

        // Change the tab if there are no validation errors
        $this->currentTab = $tab;
    }

    public function updated( $propertyName ) {
        // Determine the tab and validate the current property accordingly
        if ( $this->currentTab == 1 ) {
            $this->validateOnly( $propertyName, $this->tab1Rules );
        } elseif ( $this->currentTab == 2 ) {
            $this->validateOnly( $propertyName, $this->tab2Rules );
        } elseif ( $this->currentTab == 3 ) {
            $this->validateOnly( $propertyName, $this->tab3Rules );
        }
    }

    public function incrementProgress() {
        $this->progressPercent += 4;

        // Emit a Livewire event to update the progress bar in the JavaScript
        $this->dispatch( 'updateProgressBar', $this->progressPercent );
    }

    public $authorAPA;

    public function citationAPA_generator() {
        if ( !empty( $this->author2 ) ) {
            $this->authorAPA = $this->convertAuthorName( $this->author1 ). ', ' . $this->convertAuthorName( $this->author2 );
        }
        if ( !empty( $this->author2 ) && !empty( $this->author3 ) ) {
            $this->authorAPA = $this->convertAuthorName( $this->author1 ). ', ' . $this->convertAuthorName( $this->author2 ). ', ' . $this->convertAuthorName( $this->author3 );
        }
        if ( !empty( $this->author2 ) && !empty( $this->author3 ) && !empty( $this->author4 ) ) {
            $this->authorAPA = $this->convertAuthorName( $this->author1 ). ', ' . $this->convertAuthorName( $this->author2 ). ', ' . $this->convertAuthorName( $this->author3 ). ', ' . $this->convertAuthorName( $this->author4 );
        } else {
            $this->authorAPA  = $this->convertAuthorName( $this->author1 );
        }

        $publicationLocation = 'Pangasinan State University - AC';
        $retrieveURL = 'http::/ThesisKiosk.app/documents/982734';

        $year = date( 'Y', strtotime( $this->date_of_approval ) );
        $this->recommended_citation = $this->authorAPA. '('.$year.'). '. $this->title.'. '. $this->document_type.'. '.$publicationLocation.'. '.$retrieveURL;
    }

    public function convertAuthorName( $name ) {
        $fullName = explode( ' ', $name );
        if ( count( $fullName ) >= 2 ) {

            $lastName = ucfirst( array_pop( $fullName ) );
            $firstNameInitial = ucfirst( strtoupper( substr( $fullName[ 0 ], 0, 1 ) ) );

            // Format the name as 'LastName Initial.'

            $formattedName = $lastName . ' ' . $firstNameInitial . '.';
            return $name = $formattedName;
        }
    }
    public $showProgressBox = false;
    public $progressPercent = 0;
    public $progressInfo = '';
    public  $is_Success = false;

    public function closeShowProgressBox() {
        $this->showProgressBox = false;
    }

    public function uploadDocument() {

        $rules = array_merge(
            $this->tab1Rules,
            $this->tab2Rules,
            $this->tab3Rules
        );

        $checkMe = $this->validate( $rules );
        if ( $checkMe ) {
            $this->createNewDocuPostEntry();
        } else {
            session()->flash( 'message', 'missing enrty' );
        }

    }
    public $rulesDone, $validationDone, $creatingDone, $successUploading;
    public $docuReference;

    public function createNewDocuPostEntry() {
        $this->showProgressBox = true;
        $this->progressInfo = 'Opended, processing ...';
        if ( $this->user_upload ) {
            $this->pdf_path = $this->user_upload->store( 'PDF_uploads', 'public' );
        }

        do {
            $this->docuReference = Str::random( 12 );
        }
        while( DocuPost::where( 'reference', $this->docuReference )->exists() );

        $this->progressInfo = 'preparing data ...';

        $inputsOfDocu = [
            'user_id' => $this->user->id,
            'reference' => $this->docuReference,
            'title' => $this->title,
            'format' => $this->format,
            'course' => $this->bachelor_degree_value,
            'document_type' => $this->document_type,
            'date_of_approval' => $this->date_of_approval,
            'physical_description' => $this->physical_description,
            'language' => $this->language,
            'panel_chair' => $this->panel_chair,
            'advisor' => $this->advisor,
            'panel_member_1' => $this->panel_member1,
            'panel_member_2' => $this->panel_member2,
            'panel_member_3' => $this->panel_member3,
            'panel_member_4' => $this->panel_member4,
            'abstract_or_summary' => $this->abstract_or_summary,
            'author_1' => $this->author1,
            'author_2' => $this->author2,
            'author_3' => $this->author3,
            'author_4' => $this->author4,
            'keyword_1' => $this->keyword1,
            'keyword_2' => $this->keyword2,
            'keyword_3' => $this->keyword3,
            'keyword_4' => $this->keyword4,
            'keyword_5' => $this->keyword5,
            'keyword_6' => $this->keyword6,
            'keyword_7' => $this->keyword7,
            'keyword_8' => $this->keyword8,
            'recommended_citation' => $this->recommended_citation,
            'document_file_url' => $this->pdf_path,
        ];

        DocuPost::create( $inputsOfDocu );

        $this->progressInfo = 'Success';
        $this->is_Success = true;
    }

    public function render() {
        if ( auth()->check() ) {
            $idAdmin = auth()->user()->is_admin;
        } else {
            $idAdmin = false;
        }

        $layout = $idAdmin ? 'layout.admin' : 'layout.app';
        return view( 'livewire.upload-document' )->layout( $layout );
    }
}
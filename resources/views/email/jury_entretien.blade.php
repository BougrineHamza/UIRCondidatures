@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ url('/images/uir-logo.png') }}" style="width:200px;" />
        @endcomponent
    @endslot

    {{-- Body --}}
    <!-- Body here -->
    {{-- Subcopy --}}
    @slot('subcopy')
    <h1 style="text-transform: uppercase; text-align: center;">Entretien programmé</h1>
    <p style="text-align: center; color:#1c96ad;">Vous avez un nouvel entretien...</p>

    <p style="margin-top:30px;hyphens:none;">Bonjour {{$jury_name}},
        <br /><br />
    Nous tenons à vous informer qu'un candidat vient de programmer un entretien avec vous.</p>
    <br />
    <br />

    <table style="border:1px solid #bbb; width:100%; padding:15px;">
        <tbody><tr><td style="border-bottom:1px solid #bbb; padding-bottom:15px;">
        <b style="color:#1c96ad;">{{ $formation }}</b><br />
        {{ $ecole }}

        </td></tr>
        <tr><td style="padding-top:15px;">
        Candidat: <b>{{ $nom_candidat }}</b><br />
        @if($cin_candidat)
        CIN Candiat: <b>{{ $cin_candidat }}</b><br />

        Date entretien: <b style="color:#1c96ad;">{{ date('d-m-Y', strtotime($date_entretien)) }} à {{ $heure_entretien }}</b>
        @endif
        </td></tr></tbody>
    </table>
    @endslot


    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © UIR  - Université Internationale de Rabat<br /><br />
			Technopolis Rabat-Shore, Rocade Rabat-Salé.<br />
			11 100 Sala el Jadida - Maroc<br />
			Tel : +212530103000<br />
			www.UIR.ac.ma
        @endcomponent
    @endslot
@endcomponent
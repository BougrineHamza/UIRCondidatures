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
    <h1 style="text-transform: uppercase; text-align: center;">Résultat de votre Entretien</h1>
    <p style="text-align: center; color:#1c96ad;">{{ $titre_entretien }}</p>

    <p style=" margin-top:60px;hyphens:none;">
        <font style="text-transform:capitalize;">{{ $prenom }}</font> <font style="text-transform:uppercase;">{{ $nom }}</font>,<br /><br />
        Faisant suite à l’étude de votre dossier de candidature par le jury de <b>{{ $ecole }}</b> de l’Université Internationale de Rabat pour accéder au Master, la décision suite à la délibération du jury s’énonce comme suit :<br /><br />

        <table style="border:1px solid #f1f1f1; padding:10px;"><tbody><tr><td>
        @if($resultat == 1)
            <h1 style="text-align: center; hyphens: : none;">
                <font style="text-transform:capitalize;">{{ $prenom }}</font> <font style="text-transform:uppercase;">{{ $nom }}</font> est décalaré(e) <font style="color:green;">ADMIS(E)</font> au Master:<br /> <font style=" color:#1c96ad;">{{ $titre_entretien }}</font>
            </h1>

        @elseif($resultat == 4)
            <h1 style="text-align: center; hyphens: none;">
                <font style="text-transform:capitalize;">{{ $prenom }}</font> <font style="text-transform:uppercase;">{{ $nom }}</font> est décalaré(e) <font style="color:grey;">ABSENT(E)</font> au Master:<br /> <font style=" color:#1c96ad;">{{ $titre_entretien }}</font>
            </h1>
        @else
            <h1 style="text-align: center; hyphens: none;">
                <font style="text-transform:capitalize;">{{ $prenom }}</font> <font style="text-transform:uppercase;">{{ $nom }}</font> est décalaré(e) <font style="color:red;">NON ADMIS(E)</font> au Master:<br /> <font style=" color:#1c96ad;">{{ $titre_entretien }}</font>
            </h1>
        @endif
        </td></tr></tbody></table>
        <br /><br />
        <b>Votre admission reste conditionnée par la validation de votre dossier académique d’inscription par le service scolarité au moment de l’inscription.</b>
        <br /><br />
        A ce titre, il vous appartient de régulariser votre dossier administratif auprès du service scolarité de l’UIR : <a href="mailto:scolarite@uir.ac.ma">scolarite@uir.ac.ma</a>.
        <br /> <br />

        Dans l’attente de vous accueillir au sein de notre établissement, nous vous prions d’agréer, <font style="text-transform:capitalize;">{{ $prenom }}</font> <font style="text-transform:uppercase;">{{ $nom }}</font>, nos salutations distinguées.</p>

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
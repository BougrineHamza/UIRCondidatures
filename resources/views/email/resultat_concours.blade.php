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

    <p style="text-align: center; margin-top:60px;hyphens:none;">
        Nous souhaitons vous informer que la décision du jury pour votre entretien <b>{{ $titre_entretien }}</b> est:<br />

        @if($resultat == 1)
            <h1 style="color:green;text-align: center;">
                ADMIS(E)
            </h1>

        @elseif($resultat == 4)
            <h1 style="color:grey;text-align: center;">
                ABSENT(E)
            </h1>
        @else
            <h1 style="color:red;text-align: center;">
                NON ADMIS(E)
            </h1>
        @endif
        <br /><br />Nous tenons à vous rappeler qu'il est toujours possible de souscrire à d'autres entretiens.</p>
        @component('mail::button', ['url' => url('/email/mes-convocations?email='.$email.'&token='.$password.'s'),'color' => 'yellow'])
            Nouveau entretien ➝
        @endcomponent
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
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
    <h1 style="text-transform: uppercase; text-align: center;">Rappel entretien(s)</h1>
    <p style="text-align: center; color:#1c96ad;">Vos entretiens de la semaine...</p>

    <p style="text-align: center; margin-top:30px;hyphens:none;">Nous souhaitons vous rappeler que vous avez souscrit à un/des entretien(s) <u>cette semaine</u>.
        <br /><br />Nous vous prions de bien vouloir vous présenter muni(e) de votre convocation sur support papier ou numérique (appareil mobile).</p>
        @component('mail::button', ['url' => url('/email/mes-convocations?email='.$email.'&token='.$password.'s'),'color' => 'green'])
            Voir ma convocation ➝
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
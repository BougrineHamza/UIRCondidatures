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
    <h1 style="text-transform: uppercase; text-align: center;">Confirmez votre e-mail !</h1>
    <p style="text-align: center; color:#1c96ad;">Cliquez sur le lien de confirmation pour continuer...</p>

    <p style="text-align: center; margin-top:30px;hyphens:none;">Merci de confirmer votre e-mail pour valider votre compte.</p>
        @component('mail::button', ['url' => url('/email/confirmer-email?email='.$email.'&token='.$password),'color' => 'yellow'])
            Je confirme mon e-mail ➝
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
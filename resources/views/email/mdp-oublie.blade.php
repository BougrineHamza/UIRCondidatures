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
    <h1 style="text-transform: uppercase; text-align: center;">Créer un nouveau mot de passe ?</h1>
    <p style="text-align: center; color:#1c96ad;">Cliquez sur le lien de réinitialisation pour continuer...</p>

    <p style="text-align: center; margin-top:30px;hyphens:none;">Une demande de réinitialisation de mot de passe a été effectuée pour votre compte. Si vous souhaitez continuer, cliquez sur le bouton ci-dessous:</p>
        @component('mail::button', ['url' => url('/email/modifer-mdp?email='.$email.'&token='.$password),'color' => 'yellow'])
			Nouveau mot de passe ➝
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
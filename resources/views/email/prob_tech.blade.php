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
    <h1 style="text-transform: uppercase; text-align: center;">Problème Technique</h1>
    <p style="text-align: center; color:#1c96ad;">Vous avez reçu beaucoup d'emails</p>

    <p style="text-align: center; margin-top:30px;hyphens:none;">Nous sommes désolés de vous informer qu'une erreur est survenue lors de l'envoi d'emails. Vous pouvez supprimer ces e-mails sans problème.</p>

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
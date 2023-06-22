<?php
namespace App\Services\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Basic;

class MyCustomPolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        // STYLE
        $this
            // ->addDirective(Directive::STYLE, 'unsafe-hashes')
            ->addDirective(Directive::STYLE, 'https://fonts.googleapis.com')
            ->addDirective(Directive::STYLE, 'https://code.jquery.com')
            ->addDirective(Directive::STYLE, 'https://cdnjs.cloudflare.com')
            ->addDirective(Directive::STYLE, 'https://cdn.datatables.net')
            ->addNonceForDirective(Directive::STYLE);


        // SCRIPT
        $this
            // ->addDirective(Directive::SCRIPT, 'unsafe-hashes')
            ->addDirective(Directive::SCRIPT, 'https://code.jquery.com')
            ->addDirective(Directive::SCRIPT, 'https://www.gstatic.com')
            ->addDirective(Directive::SCRIPT, 'https://cdnjs.cloudflare.com')
            ->addDirective(Directive::SCRIPT, 'https://ajax.aspnetcdn.com')
            ->addDirective(Directive::SCRIPT, 'https://cdn.datatables.net')
            ->addNonceForDirective(Directive::SCRIPT);

        // Google Font
        $this
            ->addDirective(Directive::FONT, 'https://fonts.gstatic.com')
            ->addDirective(Directive::FONT, 'https://cdnjs.cloudflare.com');
    }
}

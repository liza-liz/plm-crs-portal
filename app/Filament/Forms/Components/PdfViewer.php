<?php 

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class PdfViewer extends Field
{
    protected string $view = 'filament.components.pdf-viewer';

    protected function setUp(): void
    {
        parent::setUp();

    }
}

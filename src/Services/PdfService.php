<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService {
  private $dompdf;

  public function __construct() {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', realpath(__DIR__ . '/../../'));
    $options->set('defaultFont', 'Arial');
    $this->dompdf = new Dompdf($options);
  }

  public function generatePdf($html, $filename = 'document.pdf') {
    $this->dompdf->loadHtml($html);
    $this->dompdf->setPaper('A4', 'portrait');
    $this->dompdf->render();
    $this->dompdf->stream($filename, ['Attachment' => false]);
  }
}
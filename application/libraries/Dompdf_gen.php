<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Dompdf_gen {
    /**
     * @var Dompdf
     */
    private $dompdf;

    public function __construct() {
        if ( ! class_exists(Dompdf::class)) {
            show_error('Dompdf is not installed. Run "composer install" in the project root before generating PDFs.');
        }

        $options = new Options();
        $options->set('isRemoteEnabled', FALSE);
        $options->set('isPhpEnabled', FALSE);

        $this->dompdf = new Dompdf($options);
    }

    public function set_paper($size, $orientation = 'portrait') {
        $this->dompdf->setPaper($size, $orientation);
        return $this;
    }

    public function load_html($html, $encoding = null) {
        $this->dompdf->loadHtml($html, $encoding);
        return $this;
    }

    public function render() {
        $this->dompdf->render();
        return $this;
    }

    public function stream($filename = 'document.pdf', $options = array()) {
        if ( ! isset($options['Attachment'])) {
            $options['Attachment'] = 1;
        }

        $this->dompdf->stream($filename, $options);
    }
}

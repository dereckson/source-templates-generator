<?php

trait DownloadWithWget {

    /**
     * @return string
     */
    private function getTemporaryFilename () {
        $dir = sys_get_temp_dir();
        return tempnam($dir, "http-client-wget-");
    }

    /**
     * Gets the content of the specified URL, using wget to download it
     *
     * @return string
     */
    function getFileContents ($url) {
        $file = $this->getTemporaryFilename();
        $url = escapeshellarg($url);

        system("wget -q -O $file $url");
        $data = file_get_contents($file);
        unlink($file);

        return $data;
    }

    /**
     * Downloads the URL through wget and fill data properties
     */
    function get_data () {
        $this->data = $this->getFileContents($this->url);
        $this->encodeData();
    }

}

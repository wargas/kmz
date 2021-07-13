<?php

namespace Kmz;

class Kmz
{

    private $file = null;
    private $xml = "";

    public function __construct($file)
    {
        if (file_exists($file)) {
            $this->file = $file;

            $this->openZipFile();
        } else {
            throw new \Exception("FILE NOT FOUND", 1);
        }
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function toArray()
    {
        // $data = \simplexml_load_string($this->xml);

        // $document = json_decode(json_encode($data->Document));

        // return $this->getPlacesFromFolder($document->Folder);
        $dom = new \DOMDocument;
        $dom->loadXML($this->xml);

        $elements = $dom->getElementsByTagName('Placemark');

        $data = [];
        foreach ($elements as $element) {
            $name = $element->getElementsByTagName("name");
            $longitude = $element->getElementsByTagName("longitude");
            $latitude = $element->getElementsByTagName("latitude");
            $altitude = $element->getElementsByTagName("altitude");

            if ($longitude->item(0)) {

                $data[] = [
                    "name" => $name->item(0)->textContent,
                    "longitude" => $longitude->item(0)->textContent,
                    "latitude" => $latitude->item(0)->textContent,
                    "altitude" => $altitude->item(0)->textContent,
                ];
            }
        }

        return $data;
    }

    private function getPlacesFromFolder($folder)
    {
        if (isset($folder->Placemark)) {
            return $folder->Placemark;
        }

        if (isset($folder->Folder)) {
            $subs = [];

            foreach ($folder->Folder as $item) {
                $subs = $this->getPlacesFromFolder($item);
            }

            return $subs;
        }

        return [];
    }

    private function openZipFile()
    {
        $zip = new \ZipArchive();
        $zip->open($this->file);

        $this->xml = $zip->getFromName("doc.kml");

        $zip->close();
    }
}

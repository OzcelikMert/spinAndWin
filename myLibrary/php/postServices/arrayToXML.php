<?php
namespace myLibrary\php\post_services;

use DOMDocument;
use DOMElement;
use DOMException;
use Exception;

class arrayToXML
{
    protected DOMDocument $document;

    protected bool $replace_spaces_by_under_scores_in_key_names = true;

    protected bool $add_xml_declaration = true;

    protected string $numeric_tag_name_prefix = 'numeric_';

    public function __construct(
        array $array,
        string $root_element = '',
        bool $replace_spaces_by_under_scores_in_key_names = true,
        string $xml_encoding = '',
        string $xml_version = '1.0',
        array $dom_properties = [],
        bool $xml_standalone = false
    ) {
        $this->document = new DOMDocument($xml_version, $xml_encoding);

        if (! is_null($xml_standalone)) {
            $this->document->xmlStandalone = $xml_standalone;
        }

        if (! empty($dom_properties)) {
            $this->set_dom_properties($dom_properties);
        }

        $this->replace_spaces_by_under_scores_in_key_names = $replace_spaces_by_under_scores_in_key_names;

        if ($this->is_array_all_key_sequential($array) && ! empty($array)) {
            throw new DOMException('Invalid Character Error');
        }

        $root = $this->create_root_element($root_element);

        $this->document->appendChild($root);

        $this->convert_element($root, $array);
    }

    public function set_numeric_tag_name_prefix(string $prefix)
    {
        $this->numeric_tag_name_prefix = $prefix;
    }

    public static function convert(
        array $array,
        $root_element = '',
        bool $replace_spaces_by_under_scores_in_key_names = true,
        string $xml_encoding = "",
        string $xml_version = '1.0',
        array $dom_properties = [],
        bool $xml_standalone = false
    ) {
        $converter = new static(
            $array,
            $root_element,
            $replace_spaces_by_under_scores_in_key_names,
            $xml_encoding,
            $xml_version,
            $dom_properties,
            $xml_standalone
        );

        return $converter->to_xml();
    }

    public function to_xml(): string
    {
        if ($this->add_xml_declaration === false) {
            return $this->document->saveXml($this->document->documentElement);
        }

        return $this->document->saveXML();
    }

    public function to_dom(): DOMDocument
    {
        return $this->document;
    }

    protected function ensure_valid_dom_properties(array $dom_properties)
    {
        foreach ($dom_properties as $key => $value) {
            if (! property_exists($this->document, $key)) {
                throw new Exception($key.' is not a valid property of DOMDocument');
            }
        }
    }

    public function set_dom_properties(array $dom_properties)
    {
        $this->ensure_valid_dom_properties($dom_properties);

        foreach ($dom_properties as $key => $value) {
            $this->document->{$key} = $value;
        }

        return $this;
    }

    public function prettify()
    {
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;

        return $this;
    }

    public function drop_xml_declaration()
    {
        $this->add_xml_declaration = false;

        return $this;
    }

    private function convert_element(DOMElement $element, $value)
    {
        $sequential = $this->is_array_all_key_sequential($value);

        if (! is_array($value)) {
            $value = htmlspecialchars($value);

            $value = $this->remove_control_characters($value);

            $element->nodeValue = $value;

            return;
        }

        foreach ($value as $key => $data) {
            if (! $sequential) {
                if (($key === '_attributes') || ($key === '@attributes')) {
                    $this->add_attributes($element, $data);
                } elseif ((($key === '_value') || ($key === '@value')) && is_string($data)) {
                    $element->nodeValue = htmlspecialchars($data);
                } elseif ((($key === '_cdata') || ($key === '@cdata')) && is_string($data)) {
                    $element->appendChild($this->document->createCDATASection($data));
                } elseif ((($key === '_mixed') || ($key === '@mixed')) && is_string($data)) {
                    $fragment = $this->document->createDocumentFragment();
                    $fragment->appendXML($data);
                    $element->appendChild($fragment);
                } elseif ($key === '__numeric') {
                    $this->add_numeric_node($element, $data);
                } elseif (substr($key, 0, 9) === '__custom:') {
                    $this->add_node($element, str_replace('\:', ':', preg_split('/(?<!\\\):/', $key)[1]), $data);
                } else {
                    $this->add_node($element, $key, $data);
                }
            } elseif (is_array($data)) {
                $this->add_collection_node($element, $data);
            } else {
                $this->add_sequential_node($element, $data);
            }
        }
    }

    protected function add_numeric_node(DOMElement $element, $value)
    {
        foreach ($value as $key => $item) {
            $this->convert_element($element, [$this->numeric_tag_name_prefix.$key => $item]);
        }
    }

    protected function add_node(DOMElement $element, $key, $value)
    {
        if ($this->replace_spaces_by_under_scores_in_key_names) {
            $key = str_replace(' ', '_', $key);
        }

        $child = $this->document->createElement($key);
        $element->appendChild($child);
        $this->convert_element($child, $value);
    }

    protected function add_collection_node(DOMElement $element, $value)
    {
        if ($element->childNodes->length === 0 && $element->attributes->length === 0) {
            $this->convert_element($element, $value);

            return;
        }

        $child = $this->document->createElement($element->tagName);
        $element->parentNode->appendChild($child);
        $this->convert_element($child, $value);
    }

    protected function add_sequential_node(DOMElement $element, $value)
    {
        if (empty($element->nodeValue) && ! is_numeric($element->nodeValue)) {
            $element->nodeValue = htmlspecialchars($value);

            return;
        }

        $child = new DOMElement($element->tagName);
        $child->nodeValue = htmlspecialchars($value);
        $element->parentNode->appendChild($child);
    }

    protected function is_array_all_key_sequential($value)
    {
        if (! is_array($value)) {
            return false;
        }

        if (count($value) <= 0) {
            return true;
        }

        if (\key($value) === '__numeric') {
            return false;
        }

        return array_unique(array_map('is_int', array_keys($value))) === [true];
    }

    protected function add_attributes(DOMElement $element, array $data)
    {
        foreach ($data as $attrKey => $attrVal) {
            $element->setAttribute($attrKey, $attrVal);
        }
    }

    protected function create_root_element($root_element): DOMElement
    {
        if (is_string($root_element)) {
            $root_element_name = $root_element ?: 'root';

            return $this->document->createElement($root_element_name);
        }

        $root_element_name = $root_element['rootElementName'] ?? 'root';

        $element = $this->document->createElement($root_element_name);

        foreach ($root_element as $key => $value) {
            if ($key !== '_attributes' && $key !== '@attributes') {
                continue;
            }

            $this->add_attributes($element, $root_element[$key]);
        }

        return $element;
    }

    protected function remove_control_characters(string $value): string
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    }
}

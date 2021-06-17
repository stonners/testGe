<?php namespace BunkerPalace\BunkerData\Classes;

use Cms\Classes\Controller as CmsController;

class EmbeddedPartialsParser
{
    public static function parse($markup)
    {
        $controller = CmsController::getController();
        $matches = [];

        if (preg_match_all('/\<figure\s+[^\>]+\>[^\<]*\<\/figure\>/i', $markup, $matches)) {

            foreach($matches[0] as $partialDeclaration) {

                $nameMatch = [];

                if (!preg_match('/data\-editor\-embedded\-partial\s*=\s*"([^"]+)"/', $partialDeclaration, $nameMatch)) {
                    continue;
                }

                $partialName = $nameMatch[1];

                $paramsMatch = [];

                preg_match('/data\-editor\-embedded\-partial\-params\s*=\s*"([^"]+)"/', $partialDeclaration, $paramsMatch);

                $partialParams = [];

                if (isset($paramsMatch[1])) {
                    $partialParams = json_decode(base64_decode($paramsMatch[1]), true);
                }
                
                $renderedPartial = $controller->renderPartial($partialName, $partialParams, false);
                $renderedPartial = '<div class="embedded-partial embedded-partial--' . $partialName . '">' . $renderedPartial . '</div>';
                $markup = str_replace($partialDeclaration, $renderedPartial, $markup);

            }

        }

        return $markup;
    }
}
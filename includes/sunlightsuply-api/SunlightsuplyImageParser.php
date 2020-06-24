<?php

namespace nongkuschoolubol;

class SunlightsuplyImageParser
{
    public static function getImages($page)
    {
        $preparedImages = [];

        $page = self::getParsedPage($page);

        if ($page) {
            $imgContainer = $page->find('#prodImgContainer');
            $images = json_decode($imgContainer->attr('data-content'));


            foreach ($images as $image) {
                if ($image->ParentType == 'PART')
                    $preparedImages[$image->ParentId][] = [
                        'url' => $image->UrlOriginal,
                        'is_main' => $image->ImageType == 'MAIN'
                    ];
            }
        }

        return $preparedImages;
    }

    /**
     * @param $url
     * @return mixed
     */
    protected static function getPage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param $url
     * @return \phpQueryObject|\QueryTemplatesParse|\QueryTemplatesSource|\QueryTemplatesSourceQuery
     */
    protected static function getParsedPage($url)
    {
        $html = self::getPage($url);

        return \phpQuery::newDocumentHTML($html);
    }
}
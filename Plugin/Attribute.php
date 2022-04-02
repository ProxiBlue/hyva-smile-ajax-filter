<?php
declare(strict_types=1);

namespace ProxiBlue\AjaxLayer\Plugin;

class Attribute
{
    /**
     * @param \Smile\ElasticsuiteCatalog\Block\Navigation\Renderer\Attribute $subject
     * @param $result
     * @return false|string
     */
    public function afterGetJsLayout(\Smile\ElasticsuiteCatalog\Block\Navigation\Renderer\Attribute $subject, $result)
    {
        $result = json_decode($result);
        foreach ($result->items as $item) {
            $url = $item->url;
            $parts = parse_url($url);
            if(isset($parts['query'])) {
                $filterParts = explode('=', $parts['query']);
                $item->request_var = $filterParts[0];
            }
        }
        return json_encode($result);
    }
}



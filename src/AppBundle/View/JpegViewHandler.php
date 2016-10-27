<?php

namespace AppBundle\View;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JpegViewHandler
{

    public function createResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        $csv = '<?xml version="1.0" encoding="ISO-8859-1"?>';
        $csv .= '<rss version="2.0">';
        $csv .= '<channel>';
        $csv .= '<title>My CSV</title>';
        $csv .= '<link>http://www.mywebsite.com</link>';
        $csv .= '<description>This is an example CSV</description>';
        $csv .= '<language>en-us</language>';
        $csv .= '<copyright>Copyright (C) 2009 mywebsite.com</copyright>';

        return new Response($csv, 200, $view->getHeaders());
    }

}
<?php

function renderPage($pageName, $reqData)
{
    return view('include/header')
        . view("pages/$pageName", ['data' => $reqData])
        .view('include/footer');
}

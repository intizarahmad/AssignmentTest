<?php

interface Parser {
    public function parse(Service $obj);
}
interface ServiceHandler{
    public function response(Service $obj,$array, Parser $outputParser);
}
<?php

class Ycarticle extends oxArticle
{

    public function resetLoadedParents()
    {
        static::$_aLoadedParents = array();
    }

}

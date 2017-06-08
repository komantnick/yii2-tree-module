<?php
class AdjacencyListCest 
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnPage(['adjacency-list/add-node']);
    }

    public function openContactPage(\FunctionalTester $I)
    {
        $I->see('Contact', 'h1');        
    }

}
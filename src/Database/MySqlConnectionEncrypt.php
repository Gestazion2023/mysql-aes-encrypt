<?php

namespace Gestazion\AESEncrypt\Database;

use Illuminate\Database\MySqlConnection;

use Gestazion\AESEncrypt\Database\Schema\MySqlBuilderEncrypt;
use Gestazion\AESEncrypt\Database\Query\Grammars\MySqlGrammarEncrypt as QueryGrammar;

class MySqlConnectionEncrypt extends MySqlConnection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Gestazion\AESEncrypt\Database\Query\Grammars\MySqlGrammarEncrypt
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }
}

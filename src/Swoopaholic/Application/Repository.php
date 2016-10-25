<?php
namespace Swoopaholic\Application;

interface Repository
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @return mixed
     */
    public function add($id);

    /**
     * @return void
     */
    public function commit();
}

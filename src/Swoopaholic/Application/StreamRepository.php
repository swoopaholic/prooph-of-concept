<?php
namespace Swoopaholic\Application;

interface StreamRepository
{
    public function get($id);
    public function add($id);
    public function commit();
}

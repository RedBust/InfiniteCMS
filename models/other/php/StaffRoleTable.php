<?php

/**
 * StaffRoleTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StaffRoleTable extends RecordTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object StaffRoleTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('StaffRole');
    }
}
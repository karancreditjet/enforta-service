<?php namespace Wemagine\Enforta;

interface EnfortaServiceInterface {

    /**
     *
     * This will create a new enrollment user
     * @param array
     * @return bool
     */
    public function createNewUserEnrollment($data);
}

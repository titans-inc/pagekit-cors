<?php

namespace TitansInc\CORS\Model;

use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity(tableClass="@cors_path")
 */
class Path {

    use ModelTrait;

    /**
     * @Column(type="integer") 
     * @Id 
     */
    public $id;

    /** @Column */
    public $path = '';

    /** @Column */
    public $forced_allow_origin_value = '';

    /** @Column(type="boolean") */
    public $allow_credentials = false;

    /** @Column(type="boolean") */
    public $origin_regex = false;

    /** @Column(type="integer") */
    public $max_age = 0;

    /** @Column(type="array") */
    public $allow_origin = [];

    /** @Column(type="array") */
    public $allow_headers = [];

    /** @Column(type="array") */
    public $allow_methods = [];

    /** @Column(type="array") */
    public $expose_headers = [];

    /** @Column(type="array") */
    public $hosts = [];

}
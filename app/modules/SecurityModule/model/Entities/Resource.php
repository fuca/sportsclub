<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\OneToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity;

/**
 * ORM persistable entity representing real resource at webpage
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Resource extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $link;

    /**
     * @OneToMany(targetEntity="Resource", mappedBy="parent")
     */
    protected $children;

    /**
     * @ManyToOne(targetEntity="Resource", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\Column(type="string")
     */
    protected $comment;

}

<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShippingQuery
 *
 * @ORM\Table(name="shipping_query", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class ShippingQuery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique query identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="User who sent the query"})
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_payload", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Bot specified invoice payload"})
     */
    private $invoicePayload = '';

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="User specified shipping address"})
     */
    private $shippingAddress = '';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;


}

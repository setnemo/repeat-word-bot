<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * PreCheckoutQuery
 *
 * @ORM\Table(name="pre_checkout_query", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class PreCheckoutQuery
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
     * @var string|null
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true, options={"fixed"=true,"comment"="Three-letter ISO 4217 currency code"})
     */
    private $currency;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_amount", type="bigint", nullable=true, options={"comment"="Total price in the smallest units of the currency"})
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_payload", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Bot specified invoice payload"})
     */
    private $invoicePayload = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_option_id", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Identifier of the shipping option chosen by the user"})
     */
    private $shippingOptionId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_info", type="text", length=65535, nullable=true, options={"comment"="Order info provided by the user"})
     */
    private $orderInfo;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;


}

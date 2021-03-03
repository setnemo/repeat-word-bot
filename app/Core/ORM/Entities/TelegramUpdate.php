<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * TelegramUpdate
 *
 * @ORM\Table(name="telegram_update", indexes={@ORM\Index(name="poll_id", columns={"poll_id"}), @ORM\Index(name="chat_message_id", columns={"chat_id", "message_id"}), @ORM\Index(name="chat_id", columns={"chat_id", "channel_post_id"}), @ORM\Index(name="edited_message_id", columns={"edited_message_id"}), @ORM\Index(name="edited_channel_post_id", columns={"edited_channel_post_id"}), @ORM\Index(name="chosen_inline_result_id", columns={"chosen_inline_result_id"}), @ORM\Index(name="shipping_query_id", columns={"shipping_query_id"}), @ORM\Index(name="pre_checkout_query_id", columns={"pre_checkout_query_id"}), @ORM\Index(name="message_id", columns={"message_id"}), @ORM\Index(name="poll_answer_poll_id", columns={"poll_answer_poll_id"}), @ORM\Index(name="channel_post_id", columns={"channel_post_id"}), @ORM\Index(name="inline_query_id", columns={"inline_query_id"}), @ORM\Index(name="callback_query_id", columns={"callback_query_id"})})
 * @ORM\Entity
 */
class TelegramUpdate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Update's unique identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier"})
     */
    private $chatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="message_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming message of any kind - text, photo, sticker, etc."})
     */
    private $messageId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="edited_message_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New version of a message that is known to the bot and was edited"})
     */
    private $editedMessageId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="channel_post_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming channel post of any kind - text, photo, sticker, etc."})
     */
    private $channelPostId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="edited_channel_post_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New version of a channel post that is known to the bot and was edited"})
     */
    private $editedChannelPostId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inline_query_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming inline query"})
     */
    private $inlineQueryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chosen_inline_result_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="The result of an inline query that was chosen by a user and sent to their chat partner"})
     */
    private $chosenInlineResultId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="callback_query_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming callback query"})
     */
    private $callbackQueryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="shipping_query_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming shipping query. Only for invoices with flexible price"})
     */
    private $shippingQueryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pre_checkout_query_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New incoming pre-checkout query. Contains full information about checkout"})
     */
    private $preCheckoutQueryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="poll_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="New poll state. Bots receive only updates about polls, which are sent or stopped by the bot"})
     */
    private $pollId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="poll_answer_poll_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="A user changed their answer in a non-anonymous poll. Bots receive new votes only in polls that were sent by the bot itself."})
     */
    private $pollAnswerPollId;


}

<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="forward_from_chat", columns={"forward_from_chat"}), @ORM\Index(name="reply_to_message", columns={"reply_to_message"}), @ORM\Index(name="left_chat_member", columns={"left_chat_member"}), @ORM\Index(name="migrate_to_chat_id", columns={"migrate_to_chat_id"}), @ORM\Index(name="reply_to_chat_2", columns={"reply_to_chat", "reply_to_message"}), @ORM\Index(name="forward_from", columns={"forward_from"}), @ORM\Index(name="reply_to_chat", columns={"reply_to_chat"}), @ORM\Index(name="via_bot", columns={"via_bot"}), @ORM\Index(name="migrate_from_chat_id", columns={"migrate_from_chat_id"})})
 * @ORM\Entity
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=false, options={"comment"="Unique chat identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $chatId;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique message identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sender_chat_id", type="bigint", nullable=true, options={"comment"="Sender of the message, sent on behalf of a chat"})
     */
    private $senderChatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="Unique user identifier"})
     */
    private $userId;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true, options={"comment"="Date the message was sent in timestamp format"})
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="forward_from", type="bigint", nullable=true, options={"comment"="Unique user identifier, sender of the original message"})
     */
    private $forwardFrom;

    /**
     * @var int|null
     *
     * @ORM\Column(name="forward_from_chat", type="bigint", nullable=true, options={"comment"="Unique chat identifier, chat the original message belongs to"})
     */
    private $forwardFromChat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="forward_from_message_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier of the original message in the channel"})
     */
    private $forwardFromMessageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="forward_signature", type="text", length=65535, nullable=true, options={"comment"="For messages forwarded from channels, signature of the post author if present"})
     */
    private $forwardSignature;

    /**
     * @var string|null
     *
     * @ORM\Column(name="forward_sender_name", type="text", length=65535, nullable=true, options={"comment"="Sender's name for messages forwarded from users who disallow adding a link to their account in forwarded messages"})
     */
    private $forwardSenderName;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="forward_date", type="datetime", nullable=true, options={"comment"="date the original message was sent in timestamp format"})
     */
    private $forwardDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reply_to_chat", type="bigint", nullable=true, options={"comment"="Unique chat identifier"})
     */
    private $replyToChat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reply_to_message", type="bigint", nullable=true, options={"unsigned"=true,"comment"="Message that this message is reply to"})
     */
    private $replyToMessage;

    /**
     * @var int|null
     *
     * @ORM\Column(name="via_bot", type="bigint", nullable=true, options={"comment"="Optional. Bot through which the message was sent"})
     */
    private $viaBot;

    /**
     * @var int|null
     *
     * @ORM\Column(name="edit_date", type="bigint", nullable=true, options={"unsigned"=true,"comment"="Date the message was last edited in Unix time"})
     */
    private $editDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_group_id", type="text", length=65535, nullable=true, options={"comment"="The unique identifier of a media message group this message belongs to"})
     */
    private $mediaGroupId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author_signature", type="text", length=65535, nullable=true, options={"comment"="Signature of the post author for messages in channels"})
     */
    private $authorSignature;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true, options={"comment"="For text messages, the actual UTF-8 text of the message max message length 4096 char utf8mb4"})
     */
    private $text;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entities", type="text", length=65535, nullable=true, options={"comment"="For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text"})
     */
    private $entities;

    /**
     * @var string|null
     *
     * @ORM\Column(name="caption_entities", type="text", length=65535, nullable=true, options={"comment"="For messages with a caption, special entities like usernames, URLs, bot commands, etc. that appear in the caption"})
     */
    private $captionEntities;

    /**
     * @var string|null
     *
     * @ORM\Column(name="audio", type="text", length=65535, nullable=true, options={"comment"="Audio object. Message is an audio file, information about the file"})
     */
    private $audio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document", type="text", length=65535, nullable=true, options={"comment"="Document object. Message is a general file, information about the file"})
     */
    private $document;

    /**
     * @var string|null
     *
     * @ORM\Column(name="animation", type="text", length=65535, nullable=true, options={"comment"="Message is an animation, information about the animation"})
     */
    private $animation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="game", type="text", length=65535, nullable=true, options={"comment"="Game object. Message is a game, information about the game"})
     */
    private $game;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="text", length=65535, nullable=true, options={"comment"="Array of PhotoSize objects. Message is a photo, available sizes of the photo"})
     */
    private $photo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sticker", type="text", length=65535, nullable=true, options={"comment"="Sticker object. Message is a sticker, information about the sticker"})
     */
    private $sticker;

    /**
     * @var string|null
     *
     * @ORM\Column(name="video", type="text", length=65535, nullable=true, options={"comment"="Video object. Message is a video, information about the video"})
     */
    private $video;

    /**
     * @var string|null
     *
     * @ORM\Column(name="voice", type="text", length=65535, nullable=true, options={"comment"="Voice Object. Message is a Voice, information about the Voice"})
     */
    private $voice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="video_note", type="text", length=65535, nullable=true, options={"comment"="VoiceNote Object. Message is a Video Note, information about the Video Note"})
     */
    private $videoNote;

    /**
     * @var string|null
     *
     * @ORM\Column(name="caption", type="text", length=65535, nullable=true, options={"comment"="For message with caption, the actual UTF-8 text of the caption"})
     */
    private $caption;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact", type="text", length=65535, nullable=true, options={"comment"="Contact object. Message is a shared contact, information about the contact"})
     */
    private $contact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="text", length=65535, nullable=true, options={"comment"="Location object. Message is a shared location, information about the location"})
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="venue", type="text", length=65535, nullable=true, options={"comment"="Venue object. Message is a Venue, information about the Venue"})
     */
    private $venue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="poll", type="text", length=65535, nullable=true, options={"comment"="Poll object. Message is a native poll, information about the poll"})
     */
    private $poll;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dice", type="text", length=65535, nullable=true, options={"comment"="Message is a dice with random value from 1 to 6"})
     */
    private $dice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="new_chat_members", type="text", length=65535, nullable=true, options={"comment"="List of unique user identifiers, new member(s) were added to the group, information about them (one of these members may be the bot itself)"})
     */
    private $newChatMembers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="left_chat_member", type="bigint", nullable=true, options={"comment"="Unique user identifier, a member was removed from the group, information about them (this member may be the bot itself)"})
     */
    private $leftChatMember;

    /**
     * @var string|null
     *
     * @ORM\Column(name="new_chat_title", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="A chat title was changed to this value"})
     */
    private $newChatTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="new_chat_photo", type="text", length=65535, nullable=true, options={"comment"="Array of PhotoSize objects. A chat photo was change to this value"})
     */
    private $newChatPhoto;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="delete_chat_photo", type="boolean", nullable=true, options={"comment"="Informs that the chat photo was deleted"})
     */
    private $deleteChatPhoto = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="group_chat_created", type="boolean", nullable=true, options={"comment"="Informs that the group has been created"})
     */
    private $groupChatCreated = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="supergroup_chat_created", type="boolean", nullable=true, options={"comment"="Informs that the supergroup has been created"})
     */
    private $supergroupChatCreated = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="channel_chat_created", type="boolean", nullable=true, options={"comment"="Informs that the channel chat has been created"})
     */
    private $channelChatCreated = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="migrate_to_chat_id", type="bigint", nullable=true, options={"comment"="Migrate to chat identifier. The group has been migrated to a supergroup with the specified identifier"})
     */
    private $migrateToChatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="migrate_from_chat_id", type="bigint", nullable=true, options={"comment"="Migrate from chat identifier. The supergroup has been migrated from a group with the specified identifier"})
     */
    private $migrateFromChatId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pinned_message", type="text", length=65535, nullable=true, options={"comment"="Message object. Specified message was pinned"})
     */
    private $pinnedMessage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="invoice", type="text", length=65535, nullable=true, options={"comment"="Message is an invoice for a payment, information about the invoice"})
     */
    private $invoice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="successful_payment", type="text", length=65535, nullable=true, options={"comment"="Message is a service message about a successful payment, information about the payment"})
     */
    private $successfulPayment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="connected_website", type="text", length=65535, nullable=true, options={"comment"="The domain name of the website on which the user has logged in."})
     */
    private $connectedWebsite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="passport_data", type="text", length=65535, nullable=true, options={"comment"="Telegram Passport data"})
     */
    private $passportData;

    /**
     * @var string|null
     *
     * @ORM\Column(name="proximity_alert_triggered", type="text", length=65535, nullable=true, options={"comment"="Service message. A user in the chat triggered another user's proximity alert while sharing Live Location."})
     */
    private $proximityAlertTriggered;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reply_markup", type="text", length=65535, nullable=true, options={"comment"="Inline keyboard attached to the message"})
     */
    private $replyMarkup;


}

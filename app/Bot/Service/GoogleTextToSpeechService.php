<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use RepeatBot\Common\Singleton;

/**
 * Class GoogleTextToSpeechService
 * @package RepeatBot\Bot\Service
 */
class GoogleTextToSpeechService extends Singleton
{
    private ?TextToSpeechClient $client = null;
    private ?VoiceSelectionParams $voice = null;
    private ?AudioConfig $audioConfig = null;
    private ?SynthesisInput $synthesisInputText = null;

    private string $prefix;
    private bool $filesExists = false;

    /**
     * @param string                    $voiceName
     * @param string                    $language
     * @param TextToSpeechClient|null   $textToSpeechClient
     * @param VoiceSelectionParams|null $voiceSelectionParams
     * @param AudioConfig|null          $audioConfig
     * @param SynthesisInput|null       $synthesisInput
     * @param bool                      $filesExists
     *
     * @return $this
     */
    public function init(
        string $voiceName,
        string $language = 'en-US',
        TextToSpeechClient $textToSpeechClient = null,
        VoiceSelectionParams $voiceSelectionParams = null,
        AudioConfig $audioConfig = null,
        SynthesisInput $synthesisInput = null,
        bool $filesExists = null
    ): GoogleTextToSpeechService {
        $this->prefix = "/app/words/$language/$voiceName/";
        if (!file_exists($this->prefix)) {
            mkdir($this->prefix, 0755, true);
            clearstatcache();
        }
        if (null === $this->client) {
            $this->client = $textToSpeechClient ?? new TextToSpeechClient();
        }
        if (null === $this->voice) {
            $this->voice = $voiceSelectionParams ?? (new VoiceSelectionParams())
                    ->setLanguageCode($language)
                    ->setName($voiceName);
        }
        if (null === $this->audioConfig) {
            $this->audioConfig = $audioConfig ?? (new AudioConfig())
                    ->setAudioEncoding(AudioEncoding::MP3)
                    ->setEffectsProfileId(['telephony-class-application'])
                    ->setPitch(-4.40)
                    ->setSpeakingRate(0.89);
        }
        if (null === $this->synthesisInputText) {
            $this->synthesisInputText = $synthesisInput ?? new SynthesisInput();
        }
        if (null !== $filesExists) {
            $this->filesExists = $filesExists;
        }

        return $this;
    }

    public function getMp3(string $text): string
    {
        $path = $this->prefix . $text . '.mp3';
        if (file_exists($path) || $this->filesExists) {
            return $path;
        }

        if (
            null !== $this->client &&
            null !== $this->voice &&
            null !== $this->audioConfig &&
            null !== $this->synthesisInputText
        ) {
            $this->synthesisInputText->setText($text);
            $response = $this->client->synthesizeSpeech(
                $this->synthesisInputText,
                $this->voice,
                $this->audioConfig
            );
            $audioContent = $response->getAudioContent();

            file_put_contents($path, $audioContent);
            clearstatcache();
        }

        return $path;
    }
}

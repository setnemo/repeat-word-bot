<?php
declare(strict_types = 1);

namespace RepeatBot\Bot\Service;

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

class GoogleTextToSpeechService
{
    /**
     * @var TextToSpeechClient
     */
    private TextToSpeechClient $client;
    /**
     * @var VoiceSelectionParams
     */
    private VoiceSelectionParams $voice;
    /**
     * @var AudioConfig
     */
    private AudioConfig $audioConfig;
    /**
     * @var SynthesisInput
     */
    private SynthesisInput $synthesisInputText;
    private string $prefix;
    
    public function __construct(string $voiceName, string $language = 'en-US')
    {
        $this->prefix = "/app/words/$language/$voiceName/";
        if (!file_exists($this->prefix)) {
            mkdir($this->prefix, 0755, true);
            clearstatcache();
        }
        $this->client = new TextToSpeechClient();
        $this->voice = (new VoiceSelectionParams())
            ->setLanguageCode($language)
            ->setName($voiceName);
        $this->audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3)
            ->setEffectsProfileId(['telephony-class-application'])
            ->setPitch(-4.40)
            ->setSpeakingRate(0.89)
        ;
        $this->synthesisInputText = (new SynthesisInput());

    }
    
    public function getMp3(string $text): string
    {
        $path = $this->prefix . $text . '.mp3';
        if (file_exists($path)) {
            return $path;
        }

        $this->synthesisInputText->setText($text);
        $response = $this->client->synthesizeSpeech(
            $this->synthesisInputText,
            $this->voice,
            $this->audioConfig
        );
        $audioContent = $response->getAudioContent();

        file_put_contents($path, $audioContent);
        clearstatcache();

        return $path;
    }
}

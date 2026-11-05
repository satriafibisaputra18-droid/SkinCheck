<?php
/**
 * SkinCheck - SunscreenAnalysisException
 *
 * Custom exception thrown when a sunscreen analysis provider fails.
 */

class SunscreenAnalysisException extends Exception {
    private ?string $providerName;
    private array $context;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?string $providerName = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->providerName = $providerName;
        $this->context = $context;
    }

    public function getProviderName(): ?string {
        return $this->providerName;
    }

    public function getContext(): array {
        return $this->context;
    }
}

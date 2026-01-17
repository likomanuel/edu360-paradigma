<?php
namespace App\Services;

include_once __DIR__ . '/../../vendor/autoload.php';
use Stripe\StripeClient;
use Exception;

class StripeService
{
    /** @var StripeClient */
    private $stripe;

    public function __construct(string $secretKey)
    {
        $this->stripe = new StripeClient($secretKey);
    }

    /**
     * Creates a Stripe Checkout Session.
     *
     * @param array<string, mixed> $options Configuration options for the session
     * @return \Stripe\Checkout\Session The created checkout session object
     * @throws Exception If session creation fails
     */
    public function createCheckoutSession(array $options): \Stripe\Checkout\Session
    {
        try {
            /** @var \Stripe\Service\Checkout\SessionService $sessionService */
            $sessionService = $this->stripe->checkout->sessions;
            return $sessionService->create($options);
        } catch (Exception $e) {
            error_log("[STRIPE_SERVICE_ERROR] " . $e->getMessage());
            throw $e;
        }
    }
}

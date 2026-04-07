import PaymentController from './PaymentController'
import WebhookController from './WebhookController'

const Controllers = {
    PaymentController: Object.assign(PaymentController, PaymentController),
    WebhookController: Object.assign(WebhookController, WebhookController),
}

export default Controllers
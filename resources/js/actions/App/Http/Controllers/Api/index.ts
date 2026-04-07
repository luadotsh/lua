import WebsiteController from './WebsiteController'
import LinkController from './LinkController'
import QrcodeController from './QrcodeController'
import TagController from './TagController'
import DomainController from './DomainController'

const Api = {
    WebsiteController: Object.assign(WebsiteController, WebsiteController),
    LinkController: Object.assign(LinkController, LinkController),
    QrcodeController: Object.assign(QrcodeController, QrcodeController),
    TagController: Object.assign(TagController, TagController),
    DomainController: Object.assign(DomainController, DomainController),
}

export default Api
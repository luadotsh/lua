import AccessTokenController from './AccessTokenController'
import AuthorizationController from './AuthorizationController'
import DeviceUserCodeController from './DeviceUserCodeController'
import DeviceCodeController from './DeviceCodeController'
import TransientTokenController from './TransientTokenController'
import ApproveAuthorizationController from './ApproveAuthorizationController'
import DenyAuthorizationController from './DenyAuthorizationController'
import DeviceAuthorizationController from './DeviceAuthorizationController'
import ApproveDeviceAuthorizationController from './ApproveDeviceAuthorizationController'
import DenyDeviceAuthorizationController from './DenyDeviceAuthorizationController'

const Controllers = {
    AccessTokenController: Object.assign(AccessTokenController, AccessTokenController),
    AuthorizationController: Object.assign(AuthorizationController, AuthorizationController),
    DeviceUserCodeController: Object.assign(DeviceUserCodeController, DeviceUserCodeController),
    DeviceCodeController: Object.assign(DeviceCodeController, DeviceCodeController),
    TransientTokenController: Object.assign(TransientTokenController, TransientTokenController),
    ApproveAuthorizationController: Object.assign(ApproveAuthorizationController, ApproveAuthorizationController),
    DenyAuthorizationController: Object.assign(DenyAuthorizationController, DenyAuthorizationController),
    DeviceAuthorizationController: Object.assign(DeviceAuthorizationController, DeviceAuthorizationController),
    ApproveDeviceAuthorizationController: Object.assign(ApproveDeviceAuthorizationController, ApproveDeviceAuthorizationController),
    DenyDeviceAuthorizationController: Object.assign(DenyDeviceAuthorizationController, DenyDeviceAuthorizationController),
}

export default Controllers
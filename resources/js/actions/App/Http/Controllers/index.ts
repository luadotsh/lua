import Api from './Api'
import WorkspaceController from './WorkspaceController'
import LinkController from './LinkController'
import AnalyticsController from './AnalyticsController'
import EventController from './EventController'
import MediaController from './MediaController'
import Setting from './Setting'
import Auth from './Auth'
import RedirectController from './RedirectController'

const Controllers = {
    Api: Object.assign(Api, Api),
    WorkspaceController: Object.assign(WorkspaceController, WorkspaceController),
    LinkController: Object.assign(LinkController, LinkController),
    AnalyticsController: Object.assign(AnalyticsController, AnalyticsController),
    EventController: Object.assign(EventController, EventController),
    MediaController: Object.assign(MediaController, MediaController),
    Setting: Object.assign(Setting, Setting),
    Auth: Object.assign(Auth, Auth),
    RedirectController: Object.assign(RedirectController, RedirectController),
}

export default Controllers
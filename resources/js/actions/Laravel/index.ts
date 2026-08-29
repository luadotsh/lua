import Cashier from './Cashier'
import Horizon from './Horizon'
import Mcp from './Mcp'
import Passport from './Passport'

const Laravel = {
    Cashier: Object.assign(Cashier, Cashier),
    Horizon: Object.assign(Horizon, Horizon),
    Mcp: Object.assign(Mcp, Mcp),
    Passport: Object.assign(Passport, Passport),
}

export default Laravel
import PageController from './PageController'
import BlogController from './BlogController'
import UseCaseController from './UseCaseController'
import GlossaryController from './GlossaryController'
import ToolController from './ToolController'
import AlternativeController from './AlternativeController'

const Site = {
    PageController: Object.assign(PageController, PageController),
    BlogController: Object.assign(BlogController, BlogController),
    UseCaseController: Object.assign(UseCaseController, UseCaseController),
    GlossaryController: Object.assign(GlossaryController, GlossaryController),
    ToolController: Object.assign(ToolController, ToolController),
    AlternativeController: Object.assign(AlternativeController, AlternativeController),
}

export default Site
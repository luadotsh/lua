import DashboardStatsController from './DashboardStatsController'
import WorkloadController from './WorkloadController'
import MasterSupervisorController from './MasterSupervisorController'
import MonitoringController from './MonitoringController'
import JobMetricsController from './JobMetricsController'
import QueueMetricsController from './QueueMetricsController'
import BatchesController from './BatchesController'
import PendingJobsController from './PendingJobsController'
import CompletedJobsController from './CompletedJobsController'
import SilencedJobsController from './SilencedJobsController'
import FailedJobsController from './FailedJobsController'
import RetryController from './RetryController'
import JobsController from './JobsController'
import HomeController from './HomeController'

const Controllers = {
    DashboardStatsController: Object.assign(DashboardStatsController, DashboardStatsController),
    WorkloadController: Object.assign(WorkloadController, WorkloadController),
    MasterSupervisorController: Object.assign(MasterSupervisorController, MasterSupervisorController),
    MonitoringController: Object.assign(MonitoringController, MonitoringController),
    JobMetricsController: Object.assign(JobMetricsController, JobMetricsController),
    QueueMetricsController: Object.assign(QueueMetricsController, QueueMetricsController),
    BatchesController: Object.assign(BatchesController, BatchesController),
    PendingJobsController: Object.assign(PendingJobsController, PendingJobsController),
    CompletedJobsController: Object.assign(CompletedJobsController, CompletedJobsController),
    SilencedJobsController: Object.assign(SilencedJobsController, SilencedJobsController),
    FailedJobsController: Object.assign(FailedJobsController, FailedJobsController),
    RetryController: Object.assign(RetryController, RetryController),
    JobsController: Object.assign(JobsController, JobsController),
    HomeController: Object.assign(HomeController, HomeController),
}

export default Controllers
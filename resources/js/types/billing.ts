// Mirrors App\Models\Traits\WorkspaceUsage::usage()
// Counts are number_format()'d strings on the PHP side, percentages are numbers.
export type UsageMetric = {
    used: string;
    limit: string;
    percent: number;
    remaining: string;
    reached_limit: boolean;
};

export type UsageChart = {
    total: string;
    chart: {
        data: number[];
        label: string;
        labels: string[];
    };
};

export type UsageMetricWithChart = UsageMetric & {
    chart: UsageChart;
};

export type WorkspaceUsage = {
    billing: {
        has_subscription: boolean;
        past_due: boolean | null;
        canceled: boolean;
        active: boolean;
    };
    plan: {
        name: string;
        access_level: number;
        next_tier: { name: string } | null;
    };
    current_billing_cycle: {
        start: string;
        end: string;
    };
    current_billing_cycle_formatted: string;
    next_reset: string;
    links: UsageMetricWithChart;
    events: UsageMetricWithChart;
    domains: UsageMetric;
    tags: UsageMetric;
    users: UsageMetric;
};

// Mirrors App\Models\Plan
export type Plan = {
    id: string | number;
    name: string;
    internal_id: string;
    price: number;
    is_monthly: boolean;
    stripe_id: string;
    access_level: number;
    is_private: boolean;
    max_links: number;
    max_events: number;
    max_users: number;
    max_tags: number;
    max_domains: number;
};

export type BillingFrequency = 'monthly' | 'annually';

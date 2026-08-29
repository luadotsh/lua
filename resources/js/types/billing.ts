// Mirrors App\Models\Traits\WorkspaceUsage::usage()
// Counts come over as raw integers: a thousands separator is a rendering
// decision, and pre-formatting them made every consumer parse the string back.
export type UsageMetric = {
    used: number;
    limit: number;
    percent: number;
    remaining: number;
    reached_limit: boolean;
};

export type UsageChart = {
    total: number;
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

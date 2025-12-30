export interface User {
    id: number;
    first_name: string;
    last_name: string;
    avatar_url?: string;
    profile_photo_path?: string;
}

export interface Portfolio {
    id: number;
    title: string;
    slug: string;
    user_id: number;
    theme_settings?: any;
    custom_domain?: string;
    template_type: string;
    template_data?: any;
}

export interface Comment {
    id: number;
    user_id: number;
    content: string;
    created_at: string;
    user: User;
    replies?: Comment[];
}

export interface Post {
    id: number;
    title: string;
    content: string;
    views_count: number;
    created_at: string;
    comments_count: number;
    ratings_count?: number;
    user: User;
    portfolio: Portfolio;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedComments {
    data: Comment[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
}

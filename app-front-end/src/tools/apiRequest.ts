export type ApiSuccess<T> = {
  status: 'success';
  value: T;
};

export type ApiError = {
  status: 'error';
  message: string;
};

export type ApiResult<T> = ApiSuccess<T> | ApiError;

type RequestOptions = {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
  query?: Record<string, string | number | boolean | undefined>;
  body?: any;
  headers?: Record<string, string>;
};

export default async function apiRequest<T>(
  path: string,
  options?: RequestOptions
): Promise<ApiResult<T>> {
  try {
    let url = 'https://localhost:8001/api' + path;

    // Add query parameters if provided
    if (options?.query) {
      const params = new URLSearchParams(
        Object.entries(options.query)
          .filter(([_, v]) => v !== undefined)
          .map(([k, v]) => [k, String(v)])
      );
      url += `?${params.toString()}`;
    }

    const response = await fetch(url, {
      method: options?.method ?? 'GET',
      headers: {
        'Content-Type': 'application/json',
        ...(options?.headers || {}),
      },
      body: options?.body ? JSON.stringify(options.body) : undefined,
    });

    if (!response.ok) {
      return { status: 'error', message: response.statusText };
    }

    const text = await response.text();
    if (!text) {
      return { status: 'success', value: undefined as T };
    }

    return { status: 'success', value: JSON.parse(text) as T };
  } catch (error: any) {
    console.error(error.message);
    return { status: 'error', message: error.message };
  }
}

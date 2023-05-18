
import { Link } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    return (
        <div className="hero min-h-screen bg-base-200">
        <div className="hero-content text-center">
            <div className="max-w-md">
            <h1 className="text-5xl font-bold">About</h1>
            <p className="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi. In deleniti eaque aut repudiandae et a id nisi.</p>
            <Link href={route('dashboard')}>
                        <button className="btn btn-primary">Back to Application</button>
                    </Link>
            </div>
        </div>
        </div>
    );
}

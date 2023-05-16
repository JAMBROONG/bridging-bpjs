
import { Link } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    return (
        <div className="hero min-h-screen">
            <div className="hero-overlay bg-opacity-60"></div>
            <div className="hero-content text-center text-neutral-content">
                <div className="max-w-md">
                    <h1 className="mb-5 text-5xl font-bold">About Us</h1>
                    <p className="mb-5">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi. In deleniti eaque aut repudiandae et a id nisi.</p>
                    <Link href={route('dashboard')}>
                        <button className="btn btn-primary">Back to Application</button>
                    </Link>
                </div>
            </div>
        </div>
    );
}

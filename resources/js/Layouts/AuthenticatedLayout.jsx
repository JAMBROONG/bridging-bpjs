import { useState } from 'react';
import FooterTop from '@/Components/FooterTop';
import FooterBottom from '@/Components/FooterBottom';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import ThemeToggle from '@/Components/ThemeToggle';
export default function Authenticated({ user, header, children, navActive }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
    return (
        <div className="min-h-screen  bg-base-300">
            <nav className=" max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="navbar bg-base-300">
                    
                    <div className="navbar-start md:hidden">
                        <div className="dropdown">
                            <label tabIndex={0} className="btn btn-ghost btn-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                            </label>
                            <ul tabIndex={0} className="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
                                <li>
                                    <ResponsiveNavLink href={route('dashboard')} as="button">
                                        Dashboard
                                    </ResponsiveNavLink>
                                </li>
                                <li>
                                    <ResponsiveNavLink href={route('shifting')} as="button">
                                        Shifting
                                    </ResponsiveNavLink>
                                </li>
                                <li>
                                    <ResponsiveNavLink href={route('distribution')} as="button">
                                        Distribution
                                    </ResponsiveNavLink>
                                </li>
                                <li tabIndex={0}>
                                    <a>
                                        About
                                        <svg className="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                                    </a>
                                    <ul className="p-2 bg-base-300">
                                        <li>
                                            <ResponsiveNavLink href={route('application')} as="button">
                                                Application
                                            </ResponsiveNavLink>
                                        </li>
                                        <li><a>Developer</a></li>
                                        <li><a>Privacy & Policy</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="navbar-start hidden md:inline-flex">
                        <ul className="menu menu-horizontal px-1">
                            <li>
                                <ResponsiveNavLink href={route('dashboard')} as="button">
                                    Dashboard
                                </ResponsiveNavLink>
                            </li>
                            <li>
                                <ResponsiveNavLink href={route('shifting')} as="button">
                                    Shifting
                                </ResponsiveNavLink>
                            </li>
                            <li>
                                <ResponsiveNavLink href={route('distribution')} as="button">
                                    Distribution
                                </ResponsiveNavLink>
                            </li>
                            <li tabIndex={0}>
                                <a>
                                    About
                                    <svg className="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                                </a>
                                <ul className="p-2 bg-base-300 z-40">
                                    <li>
                                        <ResponsiveNavLink href={route('application')} as="button">
                                            Application
                                        </ResponsiveNavLink>
                                    </li>
                                    <li><a>Developer</a></li>
                                    <li><a>Privacy & Policy</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div className="navbar-end">
                        <ul className="menu menu-horizontal px-1">
                            <li>{user.name}
                            </li>
                            </ul>
                        <div className="dropdown dropdown-end">
                            <label tabIndex={0} className="btn btn-ghost btn-circle avatar">
                                <div className="w-10 rounded-full">
                                    <img src="https://e7.pngegg.com/pngimages/348/800/png-clipart-man-wearing-blue-shirt-illustration-computer-icons-avatar-user-login-avatar-blue-child.png" />
                                </div>
                            </label>
                            <ul tabIndex={0} className="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-300 rounded-box w-52">
                                <li>
                                    <ResponsiveNavLink href={route('profile.edit')} as="button">
                                        Profile
                                    </ResponsiveNavLink>
                                </li>
                                <li>
                                    <ResponsiveNavLink method="post" href={route('logout')} as="button">
                                        Log Out
                                    </ResponsiveNavLink>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )}

            <main>{children}</main>
            <FooterTop></FooterTop>
            <FooterBottom></FooterBottom>
        </div>
    );
}

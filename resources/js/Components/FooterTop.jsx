export default function FooterTop({ className = '', disabled, children, ...props }) {
    return (
        <footer
            {...props}
            className={
                `footer p-10 bg-base-300 text-base-content  max-w-7xl mx-auto px-4 sm:px-6  lg:px-8${
                    disabled && 'opacity-25'
                } ` + className
            }
            disabled={disabled}
        >
            <div className="animate__animated animate__fadeInUp animate__slow">
                <span className="footer-title">Services</span> 
                <a className="link link-hover">Branding</a> 
                <a className="link link-hover">Design</a> 
                <a className="link link-hover">Marketing</a> 
                <a className="link link-hover">Advertisement</a>
            </div> 
            <div className="animate__animated animate__fadeInUp animate__slow">
                <span className="footer-title">Company</span> 
                <a className="link link-hover">About us</a> 
                <a className="link link-hover">Contact</a> 
                <a className="link link-hover">Jobs</a> 
                <a className="link link-hover">Press kit</a>
            </div> 
            <div className="animate__animated animate__fadeInUp animate__slow">
                <span className="footer-title">Legal</span> 
                <a className="link link-hover">Terms of use</a> 
                <a className="link link-hover">Privacy policy</a> 
                <a className="link link-hover">Cookie policy</a>
            </div>
            {children}
        </footer>
    );
}

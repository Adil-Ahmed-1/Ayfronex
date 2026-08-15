// api/contact.js - Vercel Serverless Function
const nodemailer = require('nodemailer');

module.exports = async (req, res) => {
    // Allow CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    if (req.method !== 'POST') {
        return res.status(405).json({ 
            success: false, 
            message: 'Method not allowed' 
        });
    }

    const { name, email, phone, message } = req.body;

    // Validation
    if (!name || !email || !message) {
        return res.status(400).json({ 
            success: false, 
            message: 'Name, email, and message are required' 
        });
    }

    try {
        // Email transporter
        const transporter = nodemailer.createTransport({
            service: 'gmail',
            auth: {
                user: 'adward.ayfronex@gmail.com',
                pass: process.env.EMAIL_PASSWORD // Vercel Environment Variable
            }
        });

        // Email content
        const mailOptions = {
            from: `"AYFRONEX Website" <adward.ayfronex@gmail.com>`,
            to: 'adward.ayfronex@gmail.com',
            replyTo: email,
            subject: `New Contact Message from ${name}`,
            html: `
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> ${name}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Phone:</strong> ${phone || 'Not provided'}</p>
                <p><strong>Message:</strong><br>${message}</p>
                <hr>
                <p>Sent from AYFRONEX website</p>
            `,
            text: `New Contact Message\n\nName: ${name}\nEmail: ${email}\nPhone: ${phone || 'Not provided'}\nMessage: ${message}`
        };

        await transporter.sendMail(mailOptions);

        res.status(200).json({ 
            success: true, 
            message: 'Email sent successfully!' 
        });

    } catch (error) {
        console.error('Email Error:', error);
        res.status(500).json({ 
            success: false, 
            message: 'Failed to send email. Please try again.' 
        });
    }
};
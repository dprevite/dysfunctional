import React, { useState, useEffect } from 'react';

const generateRandomName = () => {
    const adjectives = ['swift', 'brave', 'quiet', 'bright', 'cosmic', 'digital', 'mystic', 'golden', 'silver', 'azure'];
    const nouns = ['phoenix', 'dragon', 'tiger', 'eagle', 'falcon', 'wolf', 'bear', 'lion', 'hawk', 'fox'];
    const adj = adjectives[Math.floor(Math.random() * adjectives.length)];
    const noun = nouns[Math.floor(Math.random() * nouns.length)];
    return `${adj}-${noun}`;
};

export default function ServerlessConfigGenerator() {
    const [config, setConfig] = useState({
        language: 'PHP 8.4',
        route: '',
        name: '',
        description: '',
        timeout: 60,
        method: 'GET',
        entrypoint: 'entrypoint.php'
    });

    const [generatedYAML, setGeneratedYAML] = useState('');

    useEffect(() => {
        setConfig(prev => ({
            ...prev,
            name: generateRandomName(),
            route: `/${generateRandomName()}`
        }));
    }, []);

    const languages = [
        'PHP 8.4',
        'Python',
        'Typescript',
        'Bash (Ubuntu)',
        'Bash (Alpine)'
    ];

    const methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'];

    const getRuntimeString = (lang) => {
        const runtimeMap = {
            'PHP 8.4': 'php:8.4',
            'Python': 'python:3.11',
            'Typescript': 'node:20',
            'Bash (Ubuntu)': 'bash:ubuntu',
            'Bash (Alpine)': 'bash:alpine'
        };
        return runtimeMap[lang] || 'php:8.4';
    };

    const generateYAML = () => {
        const yaml = `function:
  name: ${config.name}
  description: ${config.description || 'A serverless function'}
  route: ${config.route}
  method: ${config.method}
  runtime: ${getRuntimeString(config.language)}
  entrypoint: ${config.entrypoint}
  timeout: ${config.timeout}
  source: ./src

docker:
  volumes:
    - ./cache:/cache
  labels:
    - "dysfunctional"`;

        setGeneratedYAML(yaml);
    };

    const handleChange = (field, value) => {
        setConfig(prev => ({ ...prev, [field]: value }));
    };

    const handleRandomName = () => {
        setConfig(prev => ({ ...prev, name: generateRandomName() }));
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(generatedYAML);
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 p-8">
            <div className="max-w-4xl mx-auto">
                <h1 className="text-4xl font-bold text-white mb-2">Serverless Function Generator</h1>
                <p className="text-purple-200 mb-8">Configure your serverless function and generate YAML config</p>

                <div className="grid md:grid-cols-2 gap-8">
                    {/* Form Section */}
                    <div className="bg-white/10 backdrop-blur-lg rounded-lg p-6 border border-white/20">
                        <h2 className="text-xl font-semibold text-white mb-4">Configuration</h2>

                        <div className="space-y-4">
                            {/* Language */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Language
                                </label>
                                <select
                                    value={config.language}
                                    onChange={(e) => handleChange('language', e.target.value)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                >
                                    {languages.map(lang => (
                                        <option key={lang} value={lang}>{lang}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Route */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Route
                                </label>
                                <input
                                    type="text"
                                    value={config.route}
                                    onChange={(e) => handleChange('route', e.target.value)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono"
                                    placeholder="/something/{id}/thing"
                                />
                            </div>

                            {/* Name */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Name
                                </label>
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        value={config.name}
                                        onChange={(e) => handleChange('name', e.target.value)}
                                        className="flex-1 px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Function name"
                                    />
                                    <button
                                        onClick={handleRandomName}
                                        className="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition"
                                        title="Generate random name"
                                    >
                                        🎲
                                    </button>
                                </div>
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Description
                                </label>
                                <textarea
                                    value={config.description}
                                    onChange={(e) => handleChange('description', e.target.value)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="Describe your function..."
                                    rows="3"
                                />
                            </div>

                            {/* Timeout */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Timeout (seconds)
                                </label>
                                <input
                                    type="number"
                                    value={config.timeout}
                                    onChange={(e) => handleChange('timeout', parseInt(e.target.value) || 60)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    min="1"
                                    max="900"
                                />
                            </div>

                            {/* HTTP Method */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    HTTP Method
                                </label>
                                <select
                                    value={config.method}
                                    onChange={(e) => handleChange('method', e.target.value)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                >
                                    {methods.map(method => (
                                        <option key={method} value={method}>{method}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Entrypoint */}
                            <div>
                                <label className="block text-sm font-medium text-purple-200 mb-2">
                                    Entrypoint
                                </label>
                                <input
                                    type="text"
                                    value={config.entrypoint}
                                    onChange={(e) => handleChange('entrypoint', e.target.value)}
                                    className="w-full px-4 py-2 bg-slate-800 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="entrypoint.php"
                                />
                            </div>

                            {/* Generate Button */}
                            <button
                                onClick={generateYAML}
                                className="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg transition shadow-lg"
                            >
                                Generate YAML
                            </button>
                        </div>
                    </div>

                    {/* Output Section */}
                    <div className="bg-white/10 backdrop-blur-lg rounded-lg p-6 border border-white/20">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-xl font-semibold text-white">Generated Config</h2>
                            {generatedYAML && (
                                <button
                                    onClick={copyToClipboard}
                                    className="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition"
                                >
                                    Copy
                                </button>
                            )}
                        </div>

                        {generatedYAML ? (
                            <pre className="bg-slate-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm font-mono border border-green-500/30">
                {generatedYAML}
              </pre>
                        ) : (
                            <div className="bg-slate-900 text-slate-500 p-4 rounded-lg text-center border border-slate-700">
                                Click "Generate YAML" to see your configuration
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

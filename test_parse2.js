const sectionsRaw = String.raw`[{"id":"01kwbn1r1mmqfngfab4452a7hm","cv_id":"01kwbn1r14xxh7hnhz6ynj24v8","type":"target_job","title":"Target Job","content":{"job_title":"Machine Learning Engineer","job_company":"Nvidia","job_description":"NVIDIA is seeking a passionate Machine Learning Engineer to join our core AI infrastructure team and push the boundaries of performance on NVIDIA GPUs. In this role, you will design, train, and deploy state-of-the-art deep learning models, including Transformers and Large Language Models (LLMs), across massive-scale GPU clusters. You will be responsible for profiling and optimizing machine learning workloads to maximize computational efficiency using NVIDIA's proprietary software stack, including CUDA, TensorRT, cuDNN, and Triton Inference Server. Additionally, you will develop distributed training strategies\u2014such as data, tensor, and pipeline parallelism\u2014and collaborate directly with hardware architects and AI researchers to transition complex ML prototypes into robust, production-ready enterprise solutions.\n\nThe ideal candidate must hold a BS, MS, or PhD in Computer Science or a related quantitative field, backed by over three years of professional machine learning engineering experience. We require exceptional programming skills in Python and C++, along with deep, hands-on expertise in modern deep learning frameworks such as PyTorch or TensorFlow. A strong foundational knowledge of neural network architectures and experience with distributed multi-GPU computing environments are essential. Candidates will stand out if they possess direct experience with CUDA, parallel programming, or optimizing Generative AI models for inference latency and throughput, as well as familiarity with containerized MLOps orchestration platforms like Kubernetes and Docker."},"order":5,"created_at":"2026-06-30T06:56:44.000000Z","updated_at":"2026-06-30T09:12:11.000000Z","last_saved_at":"2026-06-30 09:12:11"}]`;

function parseCvSections(sectionsRaw) {
    if (!sectionsRaw) return { text: '', targetJob: {} };
    try {
        const sections = JSON.parse(sectionsRaw);
        let resumeText = '';
        let targetJob = { job_title: '', job_company: '', job_description: '' };

        function extractTextFromContent(content) {
            if (!content) return '';
            if (typeof content === 'string') return content;
            if (Array.isArray(content)) {
                return content.map(extractTextFromContent).filter(Boolean).join('\n');
            }
            if (typeof content === 'object') {
                return Object.values(content).map(extractTextFromContent).filter(Boolean).join(' | ');
            }
            return String(content);
        }

        sections.forEach(sec => {
            if (sec.type === 'target_job') {
                targetJob.job_title = sec.content?.job_title || '';
                targetJob.job_company = sec.content?.job_company || '';
                targetJob.job_description = sec.content?.job_description || '';
            } else {
                if (sec.content) {
                    resumeText += extractTextFromContent(sec.content) + '\n\n';
                }
            }
        });
        return { text: resumeText.trim(), targetJob: targetJob };
    } catch (e) {
        console.error("Failed to parse sections", e);
        return { text: '', targetJob: {} };
    }
}

console.log(parseCvSections(sectionsRaw));

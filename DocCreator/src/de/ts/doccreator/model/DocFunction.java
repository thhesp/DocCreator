package de.ts.doccreator.model;

import java.util.ArrayList;

import de.ts.doccreator.model.DocUtil.LANGUAGE;

public class DocFunction extends DocComment {
	private String filename;
	private LANGUAGE lang;
	private ArrayList<DocVar> parameters;
	private String returnType;
	
	public DocFunction(String name){
		setName(name);
		setType("FUNCTION");
		parameters = new ArrayList<DocVar>();
	}
	
	public DocFunction(String name, String description){
		setName(name);
		setDescription(description);
		setType("FUNCTION");
		parameters = new ArrayList<DocVar>();
	}
	
	public DocFunction(String name,String description, String type){
		setName(name);
		setDescription(description);
		setType(type);
		parameters = new ArrayList<DocVar>();
	}
	
	public void setFilename(String filename){
		this.filename = filename;
	}
	
	public void setLanguage(String language){
		language = language.toUpperCase();
		try{
			this.lang = LANGUAGE.valueOf(language);
		}catch(IllegalArgumentException e){
			this.lang = LANGUAGE.UNKNOWN;
		}
	}
	
	public void addParameters(String name, String type){
		parameters.add(new DocVar(name, type));
	}
	
	public void setReturnType(String returnType){
		this.returnType = returnType;
	}

	public String generateHTML(String template) {
		String result = template;
		result = result.replace("{Functionname}", name);
		result = result.replace("{Description}", description);
		result = result.replace("{Language}", lang.name());
		result = result.replace("{ReturnType}", returnType);
		
		int start = result.indexOf("{@Parameters");
		int end = result.indexOf("@}", start);
		String paramTemplate = result.substring(start, end+2);
		String parametersString = generateParametersString(paramTemplate);
		
		template = template.replace(paramTemplate, parametersString);
		
		return result;
	}
	
	public String generateSummaryHTML(String template, String color){
		String result = template;
		result = result.replace("{Color}", color);
		result = result.replace("{Functionname}", name);
		result = result.replace("{ReturnType}", returnType);
		
		int start = result.indexOf("{@Parameters");
		int end = result.indexOf("@}", start);
		String paramTemplate = result.substring(start, end+2);
		String parametersString = generateParametersString(paramTemplate);
		
		template = template.replace(paramTemplate, parametersString);
		
		return result;
	}
	
	private String generateParametersString(String paramTemplate){
		if(parameters.size() == 0){
			return "";
		}
		
		String pureTemplate = paramTemplate.replace("{@Parameters", "").replace("@}", "");
		pureTemplate = pureTemplate.trim();
		String result = "";
		for(DocVar var : parameters){
			result += pureTemplate.replace("{Paramtype}", var.getType()).replace("{Paramname}", var.getName()) + " , ";
		}
		if(result.lastIndexOf(" , ") != -1){
			int index = result.lastIndexOf(" , ");
			result = result.substring(index);
		}
		
		
		return result;
	}
	
}

package de.ts.doccreator.model;

import java.util.ArrayList;
import java.util.Date;

public class DocFile extends DocComment {
	
	private ArrayList<DocVar> constants;
	private ArrayList<DocFunction> functions;
	
	public DocFile(String name){
		setName(name);
		setType("FILE");
		constants = new ArrayList<DocVar>();
		functions = new ArrayList<DocFunction>();
	}
	
	public DocFile(String name, String description){
		setName(name);
		setDescription(description);
		setType("FILE");
		constants = new ArrayList<DocVar>();
		functions = new ArrayList<DocFunction>();
	}
	
	public DocFile(String name,String description, String type){
		setName(name);
		setDescription(description);
		setType(type);
		constants = new ArrayList<DocVar>();
		functions = new ArrayList<DocFunction>();
	}
	
	public void addConstants(String name, String type){
		constants.add(new DocVar(name, type));
	}
	
	public void addFunction(DocFunction function){
		functions.add(function);
	}
	

	public String generateHTML(String fileTemplate, String functionTemplate, String summaryTemplate) {
		String html = fileTemplate.replace("{Filename}", name);
		summaryTemplate = summaryTemplate.replace("{Filename}", name);
		String timestamp = new Date().toString();
		String path = "";
		String summary = generateMethodSummary(summaryTemplate);
		String details = generateMethodDetails(functionTemplate);
		
		html = html.replace("{Timestamp}", timestamp);
		html = html.replace("{Path}", path);
		html = html.replace("{Description}", description);
		html = html.replace("{MethodSummary}", summary);
		html = html.replace("{MethodDetails}", details);
		
		return html;
	}
	
	private String generateMethodSummary(String summaryTemplate){
		String result = "";
		int count = 0;
		for(DocFunction func : functions){
			String color = "rowColor";
			if(count % 2 == 0) color = "altColor";
			result += func.generateSummaryHTML(summaryTemplate, color) + " ";
		}
		
		return result;
	}
	
	private String generateMethodDetails(String functionTemplate){
		String result = "";
		
		for(DocFunction func : functions){
			result += func.generateHTML(functionTemplate);
		}
		
		return result;
	}
	
	public String generateOverviewFrameLink(String template){
		String result = "";
		
		result = template.replace("{Filename}", name);
		
		return result;
	}
	
	public String generateOverviewSummaryElement(String template, String color){
		String result="";
		result = template.replace("{Filename}", name);
		result = result.replace("{Description}", description);
		result = result.replace("{Color}", color);
		return result;
	}

}
